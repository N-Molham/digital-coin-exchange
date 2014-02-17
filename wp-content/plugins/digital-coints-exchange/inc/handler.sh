#!/bin/bash

args=("$@")
args_num=${#args[@]}
final=""

for (( i=0;i<$args_num;i++)); do
	final=$final" "${args[${i}]}
done 

eval $final