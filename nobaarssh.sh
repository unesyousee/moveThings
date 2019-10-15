#!/usr/bin/expect -f
spawn ssh nobaar@nobaar "ls "
expect "@nobaar12345"
send "@nobaar12345"
interact
