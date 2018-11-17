#!/bin/bash
cd data/decisionamm-intrant-format-xml-20181017

myfunc(){
    echo "debut" > _$1.log;
    for x in $(seq 0 20); do
        begin=$(($x*50+1));
        end=$((($x+1)*50));
        echo $1 $begin $end
        ../../bin/console ephy $1 $begin $end >> _$1.log;
    done



}

for file in *.xml; do
    myfunc $file;
done
