import {Action} from 'redux';

export const FETCH_SITES = Symbol('@@heimdall/fetch_sites');
export const FETCH_SITES_SUCCESS = Symbol('@@heimdall/fetch_sites_success');
export const FETCH_SITES_ERROR = Symbol('@@heimdall/fetch_sites_error');

export const fetchSites = (): Action => ({ type: FETCH_SITES });
export const fetchSitesSuccess = (): Action => ({ type: FETCH_SITES_SUCCESS });
export const fetchSitesError = (): Action => ({ type: FETCH_SITES_ERROR });
