<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultilingualPress\Cache\Item;

interface CacheItem {

	const DEFAULT_TIME_TO_LIVE = 3600;

	/**
	 * Cache item key
	 *
	 * @return string
	 */
	public function key(): string;

	/**
	 * Cache item value.
	 *
	 * @return mixed Should be null when no value is stored in cache.
	 */
	public function value();

	/**
	 * Check if the cache item was a hit. Necessary to disguise null values stored in cache.
	 *
	 * @return bool
	 */
	public function is_hit(): bool;

	/**
	 * Check if the cache item is expired.
	 *
	 * @return bool
	 */
	public function is_expired(): bool;

	/**
	 * Sets the value for the cache item.
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function set( $value ): bool;

	/**
	 * Delete the cache item from its storage and ensure that next value() call return null.
	 *
	 * @return bool
	 */
	public function delete(): bool;

	/**
	 * Sets a specific date of expiration of the item.
	 *
	 * @param \DateTimeInterface $expire_date
	 *
	 * @return CacheItem
	 */
	public function expires_on( \DateTimeInterface $expire_date ): CacheItem;

	/**
	 * Expiration the item after a given number of seconds.
	 *
	 * @param int $time_to_live
	 *
	 * @return CacheItem
	 */
	public function expires_after( int $time_to_live ): CacheItem;

}