#!/bin/bash
cd data/decisionamm-intrant-format-xml-20181017
find . -iname '*.xml' -exec ../../bin/console ephy {} \;
