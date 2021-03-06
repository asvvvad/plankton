<?php

namespace Plankton\OAuth2\Grant;


class ClientCredentialsGrant implements Grant{
	const GRANT_TYPE_CLIENT_CREDENTIALS	= "client_credentials";
	const GRANT_TYPE_REFRESH_TOKEN 		= "refresh_token";
	const ERROR_INVALID_REQUEST 		= "invalid_request";
	const ERROR_INVALID_CLIENT 			= "invalid_client";
	const ERROR_INVALID_GRANT 			= "invalid_grant";
	const ERRORINVALID_SCOPE 			= "invalid_scope";
	const ERROR_UNAUTHORIZED_CLIENT 	= "unauthorized_client";
	const ERROR_UNSUPPORTED_GRANT_TYPE 	= "unsupported_grant_type";
}
