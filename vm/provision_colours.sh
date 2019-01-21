#!/usr/bin/env bash

reset='\e[39m'
blue='\e[96m'
green='\e[92m'
magenta='\e[95m'
yellow='\e[93m'

# Define your function here
Heading () {
    echo -e "$blue ---------------------------------------------- $reset"
    echo -e "$blue $1 $reset"
    echo -e "$blue ---------------------------------------------- $reset"
}

Title() {
    echo -e "$yellow $1 $reset"
}

Text() {
    echo -e "$blue - $1 $reset"
}

Info() {
    echo -e "$magenta -- $1 $reset"
}

Complete() {
    echo -e "$reset -- COMPLETE --------------------------------------------- $reset"
}

Gap() {
    echo -e "   "
}