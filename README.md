# Feeder for Github

**Githubで公開しているコードは開発中のバージョンです。**

開発バージョンでは、テスト中のコードも含むため意図しない不具合が発生する可能性があります。

本番環境では最新パッケージをパブリッシャーのサイトから[ダウンロード](https://walkyxwalky.com/download/feeder)してご利用ください。

## 特徴

Slim Framework 製のADR（Action-Domain-Responder）パターンを取り入れた Vue x Slim のRSSフィードリーダー（以下「本プログラム」という）です。

**本プログラムは2024年5月現在 v8.3 以上のPHPバージョンをサポートします。**

---

## 設置方法

1. 環境設定 env.sample.txt を .env にリネームしてサイト情報を編集します。
2. public内の htaccess.txt を .htaccess にリネームします。
3. feeds.json で購読するRSS情報を記載します。

```json
[
  {
    "name": "Yahoo主要",
    "src": "https://news.yahoo.co.jp/rss/topics/top-picks.xml",
    "url": "https://news.yahoo.co.jp/",
    "category": ["トピックス"]
  },
  {
    "name": "国内",
    "src": "https://news.yahoo.co.jp/rss/topics/domestic.xml",
    "url": "https://news.yahoo.co.jp/",
    "category": ["トピックス"]
  }
]
```

---

[Feeder](https://github.com/elkulo/Feeder/)  
Copyright 2020-2024 A.Sudo  
Licensed under LGPL-2.1-only  
[https://github.com/elkulo/Feeder/blob/main/LICENSE](https://github.com/elkulo/Feeder/blob/main/LICENSE)
