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

    width=$(sips -g pixelWidth "$full" | awk '/pixelWidth:/{print $2}')
    height=$(sips -g pixelHeight "$full" | awk '/pixelHeight:/{print $2}')
    longest=$(( width > height ? width : height ))

    if [ "$longest" -gt "$MAX_DIM" ]; then
      original_size=$(wc -c < "$full")
      tmp=$(mktemp "${TMPDIR:-/tmp}/optimize-images.XXXXXX").jpg
      cp "$full" "$tmp"
      sips -Z "$MAX_DIM" --setProperty formatOptions "$QUALITY" "$tmp" >/dev/null
      new_size=$(wc -c < "$tmp")
      if [ "$new_size" -lt "$original_size" ]; then
        mv "$tmp" "$full"
        echo "Resized $full ($original_size -> $new_size bytes)"
      else
        rm -f "$tmp"
        echo "Skipped resize for $full (would not shrink: $original_size -> $new_size bytes)"
      fi
    else
      echo "Skipped $full (already <= ${MAX_DIM}px, no upscale)"
    fi

    thumb="${full%.jpg}_tn.jpg"
    sips -Z "$THUMB_WIDTH" --setProperty formatOptions "$QUALITY" "$full" --out "$thumb" >/dev/null
  done
done

echo "Done. New total size:"
du -sh "$IMG_DIR"
