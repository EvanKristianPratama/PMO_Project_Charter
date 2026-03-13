import test from 'node:test';
import assert from 'node:assert/strict';

test('frontend smoke check', () => {
  const env = process.env.NODE_ENV ?? 'test';
  assert.ok(typeof env === 'string');
});
