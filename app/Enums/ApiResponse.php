<?php

namespace App\Enums;

/**
 * Configure your API response messages here.
 */
enum ApiResponse: string
{
    case RESPONSE_DELETED                      = 'Deleted';
    case RESPONSE_ERROR                        = 'Error';
    case RESPONSE_METHOD_NOT_ALLOWED           = 'Method Not Allowed';
    case RESPONSE_NOT_FOUND                    = 'Not found';
    case RESPONSE_OK                           = 'OK';
    case RESPONSE_SAVED                        = 'Saved';
    case RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS = 'Token not allowed to do this.';
    case RESPONSE_QUERY_FILTER_DISALLOWED      = 'Query filter disallowed';
    case RESPONSE_UPDATED                      = 'Updated';
}
