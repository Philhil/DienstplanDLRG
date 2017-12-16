#!/bin/bash

current_date_time="`date +%Y%m%d%H%M%S`";
git commit -am "autosave $current_date_time"
git push
