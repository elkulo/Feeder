#!/bin/sh

# feederディレクトリのZipを作成
zip -r dist/Feeder-latest-ja.zip feeder/ -x '*.git*' '*.env*' '*.DS_Store' '*__MACOSX*' '*.log*' '*/logs/' '*.sql*' '*/database/' '*/var/cache/simplepie/*.*' '*/var/cache/twig/*' '*/var/tmp/*.*'

# .htaccessをhtaccess.txtにしてZipに追加
cp -i public/.htaccess public/htaccess.txt
zip -r dist/Feeder-latest-ja.zip public/ -x '*.git*' '*.env*' '*.DS_Store' '*__MACOSX*' '*.htaccess'
rm -f public/htaccess.txt

# LICENSEをLICENSE.txtにしてZipに追加
cp -i LICENSE LICENSE.txt
zip -j dist/Feeder-latest-ja.zip LICENSE.txt
rm -f LICENSE.txt

# README.mdをZipに追加
zip -j dist/Feeder-latest-ja.zip README.md

# 完了
echo "Zip is finished!"
