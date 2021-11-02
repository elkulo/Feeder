<?php
declare(strict_types=1);

/**
 * 固有の環境変数: ENV_IDENTIFY
 * 
 * また、envファイル名を固有にすれば環境設定を分けられます。
 * 変更がない場合はコメントアウトを解除する必要はありません。
 */
// 任意の .env.example の example 部分を変更可能
//define('ENV_IDENTIFY', 'example');

/**
 * 任意なファイル名
 * 
 * Feederプログラムを公開ディレクトリ以外（httpでアクセスできない場所）に設置、
 * 本プログラム直下の bootstrap.php ファイルを require_once で読み込めば、
 * 任意のディレクトリやファイル名で実行ができます。
 */
// Feederプログラムの bootstrap.php を指定
require __DIR__ . '/../feeder/bootstrap.php';