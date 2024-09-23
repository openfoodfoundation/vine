<?php

namespace App\Enums;

/**
 * Configure your API response messages here.
 */
enum ApiResponse: string
{
    case RESPONSE_DELETED                                               = 'Deleted';
    case RESPONSE_ERROR                                                 = 'Error';
    case RESPONSE_FILE_TYPE_NOT_ALLOWED                                 = 'File type not allowed';
    case RESPONSE_METHOD_NOT_ALLOWED                                    = 'Method Not Allowed';
    case RESPONSE_NOT_FOUND                                             = 'Not found';
    case RESPONSE_OK                                                    = 'OK';
    case RESPONSE_SAVED                                                 = 'Saved';
    case RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS                          = 'Token not allowed to do this.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT                     = 'Incorrect authorization signature.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_JWT_HEADER_REQUIRED = 'JWT Authorization header required.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_IAT_CLAIM_REQUIRED  = 'IAT claim required.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_EXP_CLAIM_REQUIRED  = 'EXP claim required.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_IAT_EXPIRED         = 'IAT claim expired.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_IAT_EXP_TOO_LARGE   = 'IAT and EXP too far apart. Max diff: 1 minute.';
    case RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_EXPIRED             = 'Token expired.';
    case RESPONSE_COUNTRY_MISMATCH                                      = 'Country mismatch.';
    case RESPONSE_INVALID_MERCHANT_TEAM                                 = 'Invalid merchant team.';
    case RESPONSE_INVALID_MERCHANT_TEAM_FOR_SERVICE_TEAM                = 'Invalid merchant team for this service team. Ensure merchant team is merchant of service team.';
    case RESPONSE_QUERY_FILTER_DISALLOWED                               = 'Query filter disallowed';
    case RESPONSE_REDEMPTION_FAILED_VOUCHER_ALREADY_FULLY_REDEEMED      = 'This voucher has already been fully redeemed, no redemption made this time.';
    case RESPONSE_REDEMPTION_FAILED_REQUESTED_AMOUNT_TOO_HIGH           = 'Requested amount is greater than voucher value remaining, no redemption made this time.';
    case RESPONSE_REDEMPTION_FAILED_TOO_MANY_ATTEMPTS                   = 'Too many redemption attempts, please wait.';
    case RESPONSE_REDEMPTION_LIVE_REDEMPTION                            = 'This was a test redemption. Do NOT provide the person with goods or services.';
    case RESPONSE_REDEMPTION_TEST_REDEMPTION                            = 'Please provide the customer with their goods / services to the value of XXX.';
    case RESPONSE_REDEMPTION_SUCCESSFUL                                 = 'Redemption successful.';
    case RESPONSE_UPDATED                                               = 'Updated';
}
