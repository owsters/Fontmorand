#!/bin/bash
set -euo pipefail

IMG_DIR="web/pages/img"
MAX_DIM=1600
THUMB_WIDTH=220
QUALITY=75

for dir in ext int out grd region; do
  for full in "$IMG_DIR/$dir"/*.jpg; do
    base=$(basename "$full")
    case "$base" in *_tn.jpg) continue ;; esac
    echo "Resizing $full"
    sips -Z "$MAX_DIM" --setProperty formatOptions "$QUALITY" "$full" >/dev/null
    thumb="${full%.jpg}_tn.jpg"
    sips -Z "$THUMB_WIDTH" --setProperty formatOptions "$QUALITY" "$full" --out "$thumb" >/dev/null
  done
done

echo "Done. New total size:"
du -sh "$IMG_DIR"
