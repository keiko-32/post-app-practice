# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## このリポジトリについて

Tutorial 13〜15 の教材として使う小さな Laravel 10 投稿アプリ（post-app）です。リポジトリには 2 種類の中身があります。

- **アプリのコード**（`app/`・`routes/`・`resources/views/`・`database/` など）: 投稿の一覧・編集・削除と、Fortify による登録・ログイン
- **設計書**（Markdown と draw.io 図）: Tutorial 13 の演習成果物
  - `docs/<章番号>/` — 学習者が自分で作った設計書（例: `docs/13-8/`）。ここに追加・編集する
  - `answers/<章番号>/` — 教材の解答例。アプリとは無関係で、編集しない。Tutorial 14 以降の実装作業でも読む必要はない
  - ファイル名の先頭が題材を表す: `post-`＝この投稿アプリ、`cafe-`＝カフェのモバイルオーダー、`expense-`＝経費精算アプリ（13-8）、`register-`＝会員登録のテスト設計
  - `.drawio` を編集したら、対応する `.png` も書き出して揃える慣例

設計書・README・コミットメッセージは日本語で書かれています。

## コマンド

すべて Laravel Sail（Docker）経由で実行します。DB は MySQL（ホスト名 `mysql`）なので、ホストの `php artisan` では DB に繋がりません。

```bash
./vendor/bin/sail up -d                         # 起動
./vendor/bin/sail artisan migrate --seed        # テーブル作成＋練習データ
./vendor/bin/sail artisan test                  # 全テスト
./vendor/bin/sail artisan test tests/Feature/ExampleTest.php
./vendor/bin/sail down                          # 停止
```

- テストは `phpunit.xml` により MySQL の `testing` データベースを使う（Sail の初期化スクリプトが作成する）。
- 初回起動直後の `migrate` で「Connection refused」が出たら MySQL の起動待ちなので、少し待って再実行。
- ビューは Vite のアセットを読み込んでいないため、画面確認に `npm run dev` は不要。

## アーキテクチャ

- **認証は Laravel Fortify**（`config/fortify.php`）。有効な機能は registration と resetPasswords のみ。ログイン・登録画面は `FortifyServiceProvider` で `resources/views/auth/*.blade.php` に割り当て、ユーザー作成ロジックは `app/Actions/Fortify/CreateNewUser.php`。ログイン後のリダイレクト先は `/posts`（`fortify.home` と `RouteServiceProvider::HOME`）。
- **投稿機能**は `routes/web.php` の `auth` ミドルウェアグループ内に index / edit / update / destroy のみ（作成・詳細のルートは無い）。すべて `PostController` が担当し、バリデーションはコントローラ内の `$request->validate()` で行う（FormRequest は未使用）。
- **認可は `PostPolicy`**（投稿者本人のみ update / delete 可）。`AuthServiceProvider::$policies` は空で、Laravel のポリシー自動検出で `Post` に紐づいている。コントローラでは `$this->authorize()`、ビューでは `@can` を使い、他人の投稿の編集 URL を直接開くと 403 になる。
- **データモデル**: `User` 1—多 `Post` 多—1 `Category`。`posts.user_id` はユーザー削除で cascade、`category_id` は制約のみ。
- **シードデータ**（`DatabaseSeeder`）: カテゴリ 3 件と、`usera@example.com` / `userb@example.com`（パスワード `password`）が各 2 件の投稿を持つ。権限まわりの動作確認はこの 2 アカウントで行う。
