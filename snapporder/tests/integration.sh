#!/bin/sh
HOST="http://inside.dev"
curl -XPOST -H"Content-type: application/json; charset=UTF-8" --data-binary @testuser.json $HOST/snapporder/api/register.php


