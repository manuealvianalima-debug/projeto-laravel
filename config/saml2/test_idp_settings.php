<?php

// If you choose to use ENV vars to define these values, give this IdP its own env var names
// so you can define different values for each IdP, all starting with 'SAML2_'.$this_idp_env_id
$this_idp_env_id = 'TEST';

//This is variable is for simplesaml example only.
// For real IdP, you must set the url values in the 'idp' config to conform to the IdP's real urls.
$idp_host = env('SAML2_IDP_HOST', 'sso-homolog.fiocruz.br');

return $settings = array(

    /*****
     * One Login Settings
     */

    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true, //@todo: make this depend on laravel config

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', false),

    // Service Provider Data that we are deploying
        
    'sp' => [

    'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

    'x509cert' => '',

    'privateKey' => '',

    'entityId' => env('SAML2_SP_ENTITYID'),

    'assertionConsumerService' => [
        'url' => env('SAML2_ACS_URL'),
    ],

    'singleLogoutService' => [
        'url' => env('SAML2_SLS_URL'),
    ],

    ],

    // Identity Provider Data that we want connect with our SP
    
    
'idp' => [

    'entityId' => env('SAML2_TEST_IDP_ENTITYID'),

    'singleSignOnService' => [
        'url' => env('SAML2_TEST_IDP_SSO_URL'),
    ],

    'singleLogoutService' => [
        'url' => env('SAML2_TEST_IDP_SLO_URL'),
    ],

    'x509cert' => env('SAML2_TEST_IDP_x509'),

  //  'x509certMulti' => [
   //     'signing' => [
   //         0 => env('SAML2_'.$this_idp_env_id.'_IDP_x509_SIGNING_0', ''),
   //     ],
   //     'encryption' => [
   //         0 => env('SAML2_'.$this_idp_env_id.'_IDP_x509_ENCRYPTION_0', ''),
   //     ],
   // ],

],



    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'name',
            'emailAddress' => 'no@reply.com'
        ),
        'support' => array(
            'givenName' => 'Support',
            'emailAddress' => 'no@reply.com'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'Name',
            'displayname' => 'Display Name',
            'url' => 'http://url'
        ),
    ),

    // Configure how to map users from the IdP to users of this app.
    //'user_config' => [
    //    // By what field in the local database should the lookup occur.
    //    'checkUserBy' => 'email',
   //     // The name of the IdP attribute that maps to the field listed in "checkUserBy".
   //     'checkUserByFieldName' => 'email',
   // ],

    // Mapping from this app user fields to IdP attribute names. If the attribute
    // is missing, it is ignored.
    // Left = this app, right = simplesaml config
    //'attributes_organization' => [
    //    'email' => 'email',
    //    'firstname' => 'firstName',
    //    'lastname' => 'lastName',
   // ],

/* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current

   'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
   'wantAssertionsSigned' => true,
   'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
   'wantNameIdEncrypted' => false,
*/

);
