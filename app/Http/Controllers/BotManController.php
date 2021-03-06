<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here
     *
     * @return void
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('cryptocompare {limit}', function ($bot, $limit) {
            $result = $this->compareCryptocurrencies($limit);
            $bot->reply($result);
        });

        // Fallback in case of wrong command
        $botman->fallback(function ($bot) {
            $bot->reply("Sorry, I did not understand these commands. Try: `cryptocompare 5`");
        });

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Get comparison of cryptocurrencies from an API
     *
     * @param integer $limit
     * @return string
     */
    protected function compareCryptocurrencies($limit)
    {
        $client = new Client(['base_uri' => 'https://api.coinmarketcap.com/v1/ticker/']);

        $response = $client->get('?limit=' . $limit);
        $results = json_decode($response->getBody()->getContents());

        $data = "Here's the comparison of the top $limit cryptocurrencies: " . PHP_EOL;

        foreach ($results as $result) {
            $data .= '> ' . $result->name . ' | ' . $result->symbol . ' | ' . '$' . $result->price_usd . ' | ' . '$' . $result->market_cap_usd . PHP_EOL;
        }

        return $data;
    }
}
