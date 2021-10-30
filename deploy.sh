#!/bin/sh

# feederディレクトリのZipを作成
zip -r dist/Feeder-latest-ja.zip feeder/ -x '*.git*' '*.env*' '*.DS_Store' '*__MACOSX*' '*.log*' '*/logs/' '*.sql*' '*/database/'

# publicディレクトリをZipに追加
zip -r dist/Feeder-latest-ja.zip public/ -x '*.git*' '*.env*' '*.DS_Store' '*__MACOSX*'

# LICENSEをLICENSE.txtにしてZipに追加
cp -i LICENSE LICENSE.txt
zip -j dist/Feeder-latest-ja.zip LICENSE.txt
rm -f LICENSE.txt

# README.mdをZipに追加
zip -j dist/Feeder-latest-ja.zip README.md

# 完了
echo "Zip is finished!"
