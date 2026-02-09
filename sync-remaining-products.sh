#!/bin/bash

# Sync remaining products from offset 79 to 114
WORKER_URL="https://hercules-product-sync-uk.gilles-86d.workers.dev/sync"
AUTH_TOKEN="hercules-webhook-secret-uk-2024"
START_OFFSET=79
END_OFFSET=114

echo "Starting product sync from offset $START_OFFSET to $END_OFFSET..."
echo "=========================================="

SYNCED_COUNT=1  # We already synced offset 78
FAILED_COUNT=0

for ((offset=$START_OFFSET; offset<=$END_OFFSET; offset++)); do
  echo ""
  echo "Syncing offset $offset..."
  
  RESPONSE=$(curl -s -X POST "$WORKER_URL?offset=$offset" -H "Authorization: Bearer $AUTH_TOKEN")
  
  echo "Response: $RESPONSE"
  
  # Check if sync was successful
  if echo "$RESPONSE" | grep -q '"errors":\[\]'; then
    SYNCED_COUNT=$((SYNCED_COUNT + 1))
    echo "✅ Offset $offset synced successfully"
  else
    FAILED_COUNT=$((FAILED_COUNT + 1))
    echo "❌ Offset $offset failed"
  fi
  
  # Small delay to avoid overwhelming the worker
  sleep 2
done

echo ""
echo "=========================================="
echo "Sync Complete!"
echo "✅ Successfully synced: $SYNCED_COUNT products (including offset 78)"
echo "❌ Failed: $FAILED_COUNT products"
echo "=========================================="
