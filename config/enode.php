<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enode API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Enode API integration. You'll need to get your
    | client ID and secret from the Enode developer dashboard.
    | https://app.enode.io
    |
    */

    'client_id' => env('ENODE_CLIENT_ID'),

    'client_secret' => env('ENODE_CLIENT_SECRET'),

    'api_url' => env('ENODE_API_URL', 'https://enode-api.production.enode.io'),

    'oauth_url' => env('ENODE_OAUTH_URL', 'https://oauth.production.enode.io'),

    'redirect_uri' => env('ENODE_REDIRECT_URI', env('APP_URL').'/enode/callback'),

    'link_ui_url' => env('ENODE_LINK_UI_URL', 'https://link.enode.io'),

];
