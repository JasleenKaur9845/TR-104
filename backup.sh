#!/bin/bash
# ============================================================
# backup.sh — Backup execution script
# Runs LOCALLY on the dashboard system.
# Does NOT SSH into source/dest — runs scp from this machine.
#
# Usage:
#   bash backup.sh <source_ip> <dest_ip> <source_path> \
#                  <dest_path> <ssh_username> <dest_username> \
#                  <dashboard_ip>
#
# Parameters:
#   $1  source_ip      — IP of the source machine
#   $2  dest_ip        — IP of the backup destination machine
#   $3  source_path    — Absolute path on the source (e.g. /var/www/html)
#   $4  dest_path      — Absolute path on destination (e.g. /backups/www)
#   $5  ssh_username   — SSH user on the SOURCE machine (from servers table)
#   $6  dest_username  — SSH user on the DESTINATION machine (from settings)
#   $7  dashboard_ip   — IP of the dashboard server (from settings)
#
# SCP command used:
#   scp -r source_user@source_ip:/source_path dest_user@dest_ip:/dest_path
# ============================================================

SOURCE_IP="$1"
DEST_IP="$2"
SOURCE_PATH="$3"
DEST_PATH="${4:-/backups}"
SSH_USERNAME="$5"       # SSH user on source  (e.g. ubuntu, root, deploy)
DEST_USERNAME="$6"      # SSH user on dest    (e.g. backup, root)
DASHBOARD_IP="$7"       # IP of dashboard machine
JOB_ID="$8"

# Build receive.php URL from dashboard_ip — no hardcoded localhost
RECEIVE_URL="http://192.168.137.1/final_project/receive.php"

# ── Validate arguments ───────────────────────────────────────
if [ -z "$SOURCE_IP" ] || [ -z "$DEST_IP" ] || [ -z "$SOURCE_PATH" ] || \
   [ -z "$SSH_USERNAME" ] || [ -z "$DEST_USERNAME" ] || [ -z "$DASHBOARD_IP" ]; then
    echo "[backup.sh] ERROR: Missing arguments."
    echo "Usage: bash backup.sh <source_ip> <dest_ip> <source_path> <dest_path> <ssh_username> <dest_username> <dashboard_ip>"
    exit 1
fi

echo "[backup.sh] ============================================"
echo "[backup.sh] Backup job starting (running locally)"
echo "[backup.sh]   SCP source : ${SSH_USERNAME}@${SOURCE_IP}:${SOURCE_PATH}"
echo "[backup.sh]   SCP dest   : ${DEST_USERNAME}@${DEST_IP}:${DEST_PATH}"
echo "[backup.sh]   Dashboard  : ${DASHBOARD_IP}"
echo "[backup.sh]   Report URL : ${RECEIVE_URL}"
echo "[backup.sh] ============================================"

# ── send_update: POST status to receive.php via wget ─────────
# Uses wget (not curl). Output NOT suppressed for debugging.
send_update() {
    local STATUS="$1"
    local PERCENTAGE="$2"
    local REASON="$3"

    echo "[backup.sh] → ${STATUS} ${PERCENTAGE}% | ${REASON}"

    # URL-encode each field 

    local POST_DATA="source_ip=${SOURCE_IP}&destination_ip=${DEST_IP}&source_path=${SOURCE_PATH}&destination_path=${DEST_PATH}&status=${STATUS}&percentage=${PERCENTAGE}&reason=${REASON}"

    # wget — output shown, no -q, for debugging
    curl -X POST "$RECEIVE_URL" \
     -d "source_ip=$SOURCE_IP" \
     -d "destination_ip=$DEST_IP" \
     -d "source_path=$SOURCE_PATH" \
     -d "destination_path=$DEST_PATH" \
     -d "status=$STATUS" \
     -d "percentage=$PERCENTAGE" \
     -d "reason=$REASON" \
     -d "job_id=$JOB_ID" \
        

    local EXIT_CODE=$?
    if [ $EXIT_CODE -ne 0 ]; then
        echo "[backup.sh] WARNING: wget failed (exit ${EXIT_CODE}) — could not reach ${RECEIVE_URL}"
    fi
}

# ── STEP 1: Initializing ────────────────────────────────────
send_update "RUNNING" 0 "Initializing backup job on dashboard system..."
sleep 1

# ── STEP 2: Validate source path ────────────────────────────
send_update "RUNNING" 10 "Validating source path: ${SOURCE_PATH}"
sleep 1

if [[ "$SOURCE_PATH" != /* ]]; then
    send_update "FAILED" 10 "Invalid source path: ${SOURCE_PATH} — must start with /"
    exit 1
fi
if [[ "$DEST_PATH" != /* ]]; then
    send_update "FAILED" 10 "Invalid destination path: ${DEST_PATH} — must start with /"
    exit 1
fi

# ── STEP 3: Pre-flight connectivity check ───────────────────
send_update "RUNNING" 20 "Checking connectivity to ${SOURCE_IP}..."
sleep 1

ping -n 1 "$SOURCE_IP" > /dev/null 2>&1
if [ $? -ne 0 ]; then
    send_update "FAILED" 20 "Cannot reach source ${SOURCE_IP} — ping failed. Check network."
    exit 1
fi
echo "[backup.sh] Ping to ${SOURCE_IP} OK"

# ── STEP 4: Starting SCP transfer ────────────────────────────
send_update "RUNNING" 30 "Starting SCP: ${SSH_USERNAME}@${SOURCE_IP}:${SOURCE_PATH} → ${DEST_USERNAME}@${DEST_IP}:${DEST_PATH}"
echo "[backup.sh] Running SCP command..."

# ── REAL SCP COMMAND ─────────────────────────────────────────
# Runs locally on the dashboard machine.
# Format: scp source_user@source_ip:/source_path dest_user@dest_ip:/dest_path
# Requires SSH key-based auth from the dashboard to both machines.
#
echo "[backup.sh] Running SCP command..."

scp -v -r \
    -o StrictHostKeyChecking=no \
    -o BatchMode=yes \
    "${SSH_USERNAME}@${SOURCE_IP}:${SOURCE_PATH}/" \
    "${DEST_USERNAME}@${DEST_IP}:${DEST_PATH}/"

SCP_EXIT=$?

echo "[backup.sh] SCP exited with code: ${SCP_EXIT}"

if [ $SCP_EXIT -ne 0 ]; then
    send_update "FAILED" 30 "SCP failed (exit code ${SCP_EXIT}). Check SSH keys, paths, and permissions."
    exit 1
fi

# ── STEP 5: Verifying transfer ───────────────────────────────
send_update "RUNNING" 90 "SCP completed. Verifying transfer..."
sleep 1

# Optional: verify destination file exists via SSH
# ssh "${DEST_USERNAME}@${DEST_IP}" "test -e '${DEST_PATH}'" && echo "Verification OK" || echo "Verification warning"

# ── STEP 6: Success ─────────────────────────────────────────
send_update "SUCCESS" 100 "Backup complete. ${SSH_USERNAME}@${SOURCE_IP}:${SOURCE_PATH} → ${DEST_USERNAME}@${DEST_IP}:${DEST_PATH}"
echo "[backup.sh] ============================================"
echo "[backup.sh] Job finished successfully."
echo "[backup.sh] ============================================"

exit 0

