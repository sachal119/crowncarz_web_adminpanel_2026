<?php

namespace App\Http\Controllers;

class AccountsWebmailController extends WebmailController
{
    /**
     * Account identifier in config/webmail.php
     */
    protected string $accountKey = 'accounts';

    /**
     * Named route prefix for Accounts Webmail
     */
    protected string $routePrefix = 'accounts_emails';
}
