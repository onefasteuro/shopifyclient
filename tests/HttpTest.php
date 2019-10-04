<?php
	
namespace onefasteuro\Shopify\Tests;


	
class HttpTest extends TestCase
{


    public function testProductWebhook()
    {
        $reponse = $this->json('POST', 'api/shopify/webhooks/products/create', ['id' => 5822900033]);
        $reponse->assertStatus(200);
    }

    
    public function testCollectionWebhook()
    {
	    $reponse = $this->json('POST', 'api/shopify/webhooks/collections/update', ['id' => 229546305]);
	    $reponse->assertStatus(200);
    }

    /**
     *
     */
	public function testCollectionsList()
	{
		$response = $this->json('GET', 'api/shopify/feed/collections');
		

		$response->assertStatus(200)->assertJsonStructure([
            'channel' => [
                'item' => [
                    [
                        'title',
                        'handle',
                        'category'
                    ]
                ]
            ],
        ]);
	}


    /**
     *
     */
    public function testProductsList()
    {
        $response = $this->json('GET', 'api/shopify/feed/products');


        $response->assertStatus(200)->assertJsonStructure([
            'channel' => [
                'item' => [
                    [
                        'id',
                        'handle',
                        'title',
                        'storefront_id',
                        'type',
                        'date_created',
                        'min_price',
                        'max_price',
                        'vendor'
                    ]
                ]
            ],
        ]);
    }

	
}