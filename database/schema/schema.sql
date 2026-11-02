-- Mini Mart database schema (structure only, generated from database.sqlite)

CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "categories" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar, "icon" varchar, "image" varchar, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "contact_messages" ("id" integer primary key autoincrement not null, "name" varchar, "email" varchar, "message" text, "read" tinyint(1) not null default '0', "reply" text, "replied_at" datetime, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "delivery_staff" ("id" integer primary key autoincrement not null, "name" varchar not null, "phone" varchar not null, "vehicle" varchar not null, "status" varchar check ("status" in ('available', 'delivery', 'off')) not null default 'available', "created_at" datetime, "updated_at" datetime);

CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

CREATE TABLE "order_items" ("id" integer primary key autoincrement not null, "order_id" integer not null, "product_id" integer, "product_name" varchar not null, "unit" varchar, "price" numeric not null, "qty" numeric not null, "line_total" numeric not null, "created_at" datetime, "updated_at" datetime, foreign key("order_id") references "orders"("id") on delete cascade, foreign key("product_id") references "products"("id") on delete set null);

CREATE TABLE "orders" ("id" integer primary key autoincrement not null, "user_id" integer, "customer_name" varchar not null, "customer_email" varchar, "customer_phone" varchar, "location" varchar, "address" varchar, "truck_number" varchar, "transport_type" varchar check ("transport_type" in ('truck', 'moto')) not null default 'truck', "payment_method" varchar not null default 'Cash on delivery', "subtotal" numeric not null default '0', "delivery_fee" numeric not null default '0', "total" numeric not null default '0', "status" varchar check ("status" in ('pending', 'out', 'done')) not null default 'pending', "delivery_staff_id" integer, "placed_at" datetime, "created_at" datetime, "updated_at" datetime, "delivery_type" varchar not null default 'moto', foreign key("user_id") references "users"("id") on delete set null, foreign key("delivery_staff_id") references "delivery_staff"("id") on delete set null);

CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

CREATE TABLE "products" ("id" integer primary key autoincrement not null, "category_id" integer not null, "name" varchar not null, "price" numeric not null, "unit" varchar not null default 'each', "image" varchar, "emoji" varchar, "qty" integer not null default '0', "discount_percent" integer, "date_added" date, "created_at" datetime, "updated_at" datetime, "expiry_date" date, foreign key("category_id") references "categories"("id") on delete cascade);

CREATE TABLE "promotion_product" ("id" integer primary key autoincrement not null, "promotion_id" integer not null, "product_id" integer not null, "created_at" datetime, "updated_at" datetime, foreign key("promotion_id") references "promotions"("id") on delete cascade, foreign key("product_id") references "products"("id") on delete cascade);

CREATE TABLE "promotions" ("id" integer primary key autoincrement not null, "title" varchar, "description" text, "code" varchar, "discount_percentage" numeric, "created_at" datetime, "updated_at" datetime, "discount_percent" integer, "starts_at" date, "ends_at" date, "ended_at" datetime);

CREATE TABLE "purchase_order_items" ("id" integer primary key autoincrement not null, "purchase_order_id" integer not null, "product_id" integer, "product_name" varchar not null, "qty" integer not null, "unit_cost" numeric not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("purchase_order_id") references "purchase_orders"("id") on delete cascade, foreign key("product_id") references "products"("id") on delete set null);

CREATE TABLE "purchase_orders" ("id" integer primary key autoincrement not null, "reference_number" varchar, "supplier_name" varchar, "total_amount" numeric not null default '0', "status" varchar not null default 'pending', "created_at" datetime, "updated_at" datetime, "expected_date" date, "notes" text, "received_at" datetime);

CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

CREATE TABLE "settings" ("id" integer primary key autoincrement not null, "store_name" varchar not null default 'Mini Mart', "hours" varchar not null default '7:00 AM – 10:00 PM', "delivery_rate" numeric not null default '10', "low_stock_threshold" integer not null default '10', "created_at" datetime, "updated_at" datetime);

CREATE TABLE "users" ("id" integer primary key autoincrement not null, "first_name" varchar not null, "last_name" varchar, "email" varchar not null, "phone" varchar, "address" varchar, "email_verified_at" datetime, "password" varchar not null, "role" varchar check ("role" in ('customer', 'admin')) not null default 'customer', "remember_token" varchar, "created_at" datetime, "updated_at" datetime);

CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs" ("uuid");

CREATE INDEX "jobs_queue_index" on "jobs" ("queue");

CREATE UNIQUE INDEX "promotion_product_promotion_id_product_id_unique" on "promotion_product" ("promotion_id", "product_id");

CREATE INDEX "sessions_last_activity_index" on "sessions" ("last_activity");

CREATE INDEX "sessions_user_id_index" on "sessions" ("user_id");

CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");
