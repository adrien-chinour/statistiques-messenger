#! /bin/bash

for f in $(find $1 -name '*.json');
  do cp "$f" "$f.save"; cat "$f.save" | jq . | iconv -f utf8 -t latin1 > "$f"; rm "$f.save";
done;
