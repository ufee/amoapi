<?php
/**
 * amoCRM API client Webhooks service
 */
namespace Ufee\Amo\Services;

class Webhooks extends \Ufee\Amo\Base\Services\Cached
{
	protected
		$entity_key = 'webhooks',
		$entity_model = '\Ufee\Amo\Models\Webhook',
		$entity_collection = '\Ufee\Amo\Collections\WebhookCollection',
		$modified_from = false,
		$methods = [
			'list', 'subscriber'
		];

    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		$this->api_args = [
			'lang' => $this->instance->getAuth('lang')
		];
	}

    /**
     * Subscribe hooks in CRM
	 * @param string $url
	 * @param array $events
	 * return bool
     */
	public function subscribe($url, $events)
	{
		if (!is_string($url) || strpos($url, 'http') === false) {
			throw new \Exception('Error or invalid, "url" is required in Webhook');
		}
		if (!is_array($events) || empty($events)) {
			throw new \Exception('Error or invalid, "events" is required in Webhook');
		}
		$raw = [
			'url' => $url,
			'events' => $events
		];
		$created = $this->subscriber->subscribe([$raw]);
		return $created->first()->result;
	}

    /**
     * Unsubscribe hooks in CRM
	 * @param string $url
	 * @param array $events
	 * return bool
     */
	public function unsubscribe($url, $events)
	{
		$raw = [];
		if (is_string($url) && strpos($url, 'http') !== false) {
			$raw['url'] = $url;
		}
		if (is_array($events) && !empty($events)) {
			$raw['events'] = $events;
		}
		if (!isset($raw['url']) && !isset($raw['events'])) {
			throw new \Exception('Error or invalid, "url" or "events" is required in Webhook');
		}
		$deleted_raws = $this->subscriber->unsubscribe([$raw]);
		return $deleted_raws->first()->result;
	}

    /**
     * Get full
	 * @return Collection
     */
	public function webhooks()
	{
		return $this->list->call();
	}
}
