<?php

namespace Tests\Browser;

use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Symfony\Component\DomCrawler\Crawler;


class ScrapeTheWebTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $cats = json_decode(file_get_contents('C:\Users\effly\Downloads\open_server_5_3_5_basic_premium_ultimate\OSPanel\domains\web-shop\cats.json'), true);
            foreach ($cats as $catLink) {
               //dd('https://eme54.ru'.$catLink);


                $products = [];
                $url = $browser->visit('https://eme54.ru/'.$catLink);
                $waitContent = $url->pause(10000);

                $getPaginateLinks = $waitContent->elements('font.text a');

                $paginateLinks = [];
                foreach ($getPaginateLinks as $getPaginateLink) {
                    $paginateLinks[] = $getPaginateLink->getAttribute('href');
                }
                //dd((int)explode('?PAGEN_1=', end($paginateLinks))[1]);

                $getLinks = $waitContent->elements('.item-title');
                $links = [];
                foreach ($getLinks as $getLink) {
                    $link = $getLink->getAttribute('href');
                    $links[] = $link;
                }
                foreach ($links as $link) {

                    $url = $browser->visit($link);
                    $waitContent = $url->pause(10000);

                    $propNames = $waitContent->elements('.product__name');
                    $propValues = $waitContent->elements('.product__value');
                    $getCharNames = $waitContent->elements('.name');
                    $getCharValues = $waitContent->elements('.val');


                    foreach ($propNames as $propName) {
                        $propsNames[] = $propName->getText();
                    }
                    foreach ($propValues as $propValue) {
                        $propsValues[] = $propValue->getText();
                    }
                    $props = array_combine($propsNames, $propsValues);

                    foreach ($getCharNames as $getCharName) {
                        if ($getCharName->getText() != '') {
                            $charNames[] = $getCharName->getText();
                        }
                    }
                    foreach ($getCharValues as $getCharValue) {
                        $charValues[] = $getCharValue->getText();
                    }
                    $getCats = $waitContent->elements('a span');
                    foreach ($getCats as $item) {
                        if ($item->getText() != '') {
                            $cats[] = $item->getText();
                        }
                    }
                    //$cats[16],$cats[17],$cats[18],$cats[19];
                    $product = [
                        'firstCat' => $cats[16],
                        'secondCat' => $cats[17],
                        'thirdCat' => $cats[18],
                        'fourthCat' => $cats[19],

                        'name' => $waitContent->text('h1'),
                        'price' => $waitContent->text('.creditprice'),
                        'article' => $waitContent->text('.creditarticle'),
                        'weight' => $props["Вес, кг"],
                        'desc' => $waitContent->text('.description'),
                        'chars' => array_combine($charNames, $charValues),
                    ];

                $waitContent->assertTitle($product['name']);
                    $products[] = $product;
                }

                if (end($paginateLinks)) {
                    for ($i = 2; $i <= (int)explode('?PAGEN_1=', end($paginateLinks))[1]; $i++) {
                        $url = $browser->visit('https://eme54.ru/'. $catLink . '?PAGEN_1=' . $i);
                        $waitContent = $url->pause(10000);
                        $getLinks = $waitContent->elements('.item-title');
                        $links = [];
                        foreach ($getLinks as $getLink) {
                            $link = $getLink->getAttribute('href');
                            $links[] = $link;
                        }
                        foreach ($links as $link) {

                            $url = $browser->visit($link);
                            $waitContent = $url->pause(10000);

                            $propNames = $waitContent->elements('.product__name');
                            $propValues = $waitContent->elements('.product__value');
                            $getCharNames = $waitContent->elements('.name');
                            $getCharValues = $waitContent->elements('.val');


                            foreach ($propNames as $propName) {
                                $propsNames[] = $propName->getText();
                            }
                            foreach ($propValues as $propValue) {
                                $propsValues[] = $propValue->getText();
                            }
                            $props = array_combine($propsNames, $propsValues);

                            foreach ($getCharNames as $getCharName) {
                                if ($getCharName->getText() != '') {
                                    $charNames[] = $getCharName->getText();
                                }
                            }
                            foreach ($getCharValues as $getCharValue) {
                                $charValues[] = $getCharValue->getText();
                            }


                            $product = [
                                'firstCat' => $cats[16],
                                'secondCat' => $cats[17],
                                'thirdCat' => $cats[18],
                                'fourthCat' => $cats[19],
                                'name' => $waitContent->text('h1'),
                                'price' => $waitContent->text('.creditprice'),
                                'article' => $waitContent->text('.creditarticle'),
                                'weight' => $props["Вес, кг"],
                                'desc' => $waitContent->text('.description'),
                                'chars' => array_combine($charNames, $charValues),
                            ];
                            $waitContent->assertTitle($product['name']);
                            $products[] = $product;
                        }

                    }
            dd($products);
                }
            }
        });


    }

}
