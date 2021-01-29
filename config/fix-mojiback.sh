#! /bin/bash

for f in $(find $1 -name '*.json');
  do cp "$f" "$f.backup"; cat "$f.backup" | jq . | iconv -f utf8 -t latin1 > "$f"; rm "$f.backup";
done;
