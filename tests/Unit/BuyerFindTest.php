<?php

beforeEach(function(){    
    $this->api = init_api();    
});


test('Listar clientes cadastrados', function (){

    $response = $this->api->buyers()->list();

    expect($response !== null)->toBeTrue();
});


test('Localizar cliente pelo CPF ou CNPJ', function(){
    
    $response = $this->api->buyers()->find('157.937.650-93');

    expect($response !== null)->toBeTrue();
});