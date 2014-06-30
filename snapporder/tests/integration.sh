#!/bin/sh
HOST="https://inside.neuf.no"
curl -XPOST -H"Content-type: application/json" --data-binary @testuser.json $HOST/snapporder/api/register.php


