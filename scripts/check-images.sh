#!/bin/bash
# Enforce the never-upscale rule: every <img> in the given HTML files must have
# width/height attributes, and width must not exceed the source file's native pixels.
# Usage: scripts/check-images.sh web/index.html web/house/index.html ...
set -u
fail=0
for html in "$@"; do
  dir=$(dirname "$html")
  while IFS= read -r tag; do
    src=$(echo "$tag" | sed -n 's/.*src="\([^"]*\)".*/\1/p')
    w=$(echo "$tag" | sed -n 's/.*width="\([0-9]*\)".*/\1/p')
    h=$(echo "$tag" | sed -n 's/.*height="\([0-9]*\)".*/\1/p')
    case "$src" in http*|//*) continue ;; esac
    if [ -z "$w" ] || [ -z "$h" ]; then
      echo "FAIL $html: missing width/height on <img src=\"$src\">"; fail=1; continue
    fi
    if [ "${src#/}" != "$src" ]; then path="web${src}"; else path="$dir/$src"; fi
    if [ ! -f "$path" ]; then
      echo "FAIL $html: missing file $path"; fail=1; continue
    fi
    native=$(sips -g pixelWidth "$path" 2>/dev/null | awk '/pixelWidth/{print $2}')
    if [ -n "$native" ] && [ "$w" -gt "$native" ]; then
      echo "FAIL $html: $src width=$w exceeds native $native"; fail=1
    fi
  done < <(grep -o '<img [^>]*>' "$html")
done
[ $fail -eq 0 ] && echo "PASS: image rule holds"
exit $fail
