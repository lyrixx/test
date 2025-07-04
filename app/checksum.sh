#!/usr/bin/env sh

find . -type f -exec md5sum {} \; | md5sum
