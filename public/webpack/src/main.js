/*!
 * Feeder | el.kulo v2.2.0 (https://github.com/elkulo/Feeder/)
 * Copyright 2020-2023 A.Sudo
 * Licensed under LGPL-2.1-only (https://github.com/elkulo/Feeder/blob/main/LICENSE)
 */
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/global.scss';
import { createApp } from 'vue';
import App from './App.js';

// Twigとのコンフリクト回避.
App.delimiters = [ '${', '}' ];

// Appをマウント.
createApp( App ).mount( '#app' );
