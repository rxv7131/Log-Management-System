#!/bin/bash

# script just uses $RANDOM to make a random letter of the alphabet
# lowercase letters only, that's all that's allowed in RIT emails etc

# pick a random 1-character substring of a string that contains all the letters

letters="abcdefghijklmnopqrstuvwxyz"
echo "${letters:$(( RANDOM % ${#letters} )):1}"
