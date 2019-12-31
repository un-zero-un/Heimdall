import {useEffect} from 'react';
import {useHistory, useLocation} from 'react-router';
import {useAsyncEffect} from '../../common/hooks';
import {isAuthenticated, useJWT} from '../JWTProvider';

export default function GoogleLoginCheck() {
    const location      = useLocation();
    const history       = useHistory();
    const {setRawToken} = useJWT();
    const authenticated = isAuthenticated();

    useAsyncEffect(async () => {
        const res  = await fetch('/api/connect/google/check' + location.search);
        const json = await res.json();

        setRawToken(json.token);
    }, []);

    useEffect(() => {
        if (authenticated) {
            history.push('/');
        }
    }, [authenticated]);

    return null;
}
