## products（商品）

|カラム名|データ型|制約|説明|
|:-----|:-----|:-----|:-----|
|id|BIGINT|PRIMARY KEY, AUTO_INCREMENT|商品を1件ずつ区別する|
|name|VARCHAR(20)|NOT NULL|商品名|
|category|ENUM('drink', 'food')|NOT NULL|どのカテゴリに属するか|
|price|INT|NOT NULL|商品1個の値段|
|is_available|BOOLEAN|NOT NULL, DEFAULT true|その日売れるかどうか|
|description|VARCHAR(100)|-|商品の短い説明|
|created_at|TIMESTAMP|-|登録した日時|
|updated_at|TIMESTAMP|-|最後に書き換えた日時|


## orders (注文)

|カラム名|データ型|制約|説明|
|:-----|:-----|:-----|:-----|
|id|BIGINT|PRIMARY KEY, AUTO_INCREMENT|注文1件を区別する|
|order_number|VARCHAR(6)|NOT NULL, UNIQUE|お客さまに見せる注文番号|
|total_amount|INT|NOT NULL|注文の合計額|
|status|VARCHAR(20)|NOT NULL|注文の受け取り状態|
|ordered_at|DATETIME|NOT NULL|注文を受けた日時|
|created_at|TIMESTAMP|-|登録した日|
|updated_at|TIMESTAMP|-|最後に書き換えた日時|


## order_items (注文明細)

|カラム名|データ型|制約|説明|
|:-----|:-----|:-----|:-----|
|id|BIGINT|PRIMARY KEY, AUTO_INCREMENT|注文明細1件を区別する|
|order_id|BIGINT|NOT NULL, FOREIGN KEY|どの注文の明細か|
|product_id|BIGINT|NOT NULL, FOREIGN KEY|どの商品の明細か|
|quantity|INT|NOT NULL|注文した商品の個数|
|unit_price|INT|NOT NULL|注文した商品一個の値段|
|created_at|TIMESTAMP|-|登録した日時|
|updated_at|TIMESTAMP|-|最後に書き換えた日時|