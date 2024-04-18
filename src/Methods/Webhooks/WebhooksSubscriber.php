<?php
/**
 * amoCRM API Service method - subscribe
 */
namespace Ufee\Amo\Methods\Webhooks;

class WebhooksSubscriber extends \Ufee\Amo\Base\Methods\Post
{
	protected $url;
	
    /**
     * Subscribe hooks in CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function subscribe($raws, $arg = [])
    {
		$this->url = '/api/v2/webhooks/subscribe';

		$result = $this->call(['subscribe' => $raws], $arg);
		$result->each(function($hook) {
			if (!$hook->result) {
				if (isset($hook->notice)) {
					throw new \Exception('Error subscribe hook: '.$hook->notice);
				}
				throw new \Exception('Unknown error subscribe hook');
			}
		});
		return $result;
	}

    /**
     * Unubscribe hooks in CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function unsubscribe($raws, $arg = [])
    {
		$this->url = '/api/v2/webhooks/unsubscribe';

		$result = $this->call(['unsubscribe' => $raws], $arg);
		$result->each(function($hook) {
			if (!$hook->result) {
				if (isset($hook->notice)) {
					throw new \Exception('Error unsubscribe hook: '.$hook->notice);
				}
				throw new \Exception('Unknown error unsubscribe hook');
			}
		});
		return $result;
	}
}
