#!/bin/bash
cd decisionAMM_intrant_format_xml
find . -iname '*.xml' -exec ../bin/console ephy {} \;
