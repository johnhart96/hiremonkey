#!/bin/bash
npm install
npm run build-mac-arm64
npm run build-mac-intel
npm run dmg-arm64
npm run dmg-x64