#!/bin/bash

for x in $(seq 0 100); do
    bin/console update_bdd;
done