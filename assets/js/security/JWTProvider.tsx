import React, {createContext, ReactNode, useContext, useEffect, useState} from 'react';
import jwtDecode from 'jwt-decode';

type JWTHeaders = {
    typ: string,
    alg: string,
};

type JWTPayload = {
    iat: number,
    exp: number,
    roles: string[],
    username: string,
};

type JWTToken = {
    headers: JWTHeaders,
    payload: JWTPayload,
};

type JWTProps =
    { rawToken: string, token: JWTToken, refreshToken: string } |
    { rawToken: null, token: null, refreshToken: null };

type JWTContextProps = JWTProps & {
    setRawToken: (rawToken: string, refreshToken: string) => void,
    logout: () => void,
};

const JWTContext = createContext<JWTContextProps>({
    rawToken:     null,
    token:        null,
    refreshToken: null,
    setRawToken:  () => {
    },
    logout:       () => {
    },
});

type Props = {
    children: ReactNode,
};

export default function JWTProvider({children}: Props) {
    const [context, setContext] = useState<JWTProps>({rawToken: null, token: null, refreshToken: null});

    function setRawToken(rawToken: string, refreshToken: string) {
        const headers = jwtDecode<JWTHeaders>(rawToken, {header: true});
        const payload = jwtDecode<JWTPayload>(rawToken);

        setContext({rawToken, token: {headers, payload}, refreshToken});
        localStorage.setItem('jwt', rawToken);
        localStorage.setItem('refreshToken', refreshToken);
    }

    function logout() {
        localStorage.removeItem('jwt');
        localStorage.removeItem('refreshToken');
        setContext({rawToken: null, token: null, refreshToken: null});
    }

    async function refreshJWTToken(refreshToken: string) {
        const res  = await fetch(
            '/api/token/refresh',
            {
                method:  'POST',
                headers: {'Content-Type': 'application/json'},
                body:    JSON.stringify({refresh_token: refreshToken}),
            },
        );
        const json = await res.json();

        setRawToken(json.token, json.refresh_token);
    }

    useEffect(() => {
        const rawToken     = localStorage.getItem('jwt');
        const refreshToken = localStorage.getItem('refreshToken');
        if (null === rawToken || null === refreshToken) {
            return;
        }

        const headers = jwtDecode<JWTHeaders>(rawToken, {header: true});
        const payload = jwtDecode<JWTPayload>(rawToken);
        if (!isTokenExpired({headers, payload})) {
            setRawToken(rawToken, refreshToken);

            return;
        }

        refreshJWTToken(refreshToken);
    }, []);

    useEffect(() => {
        if (!context.refreshToken) {
            return;
        }

        const delay   = Math.round(context.token.payload.exp - ((new Date).getTime() / 1000));
        const timeout = setTimeout(async () => {
            await refreshJWTToken(context.refreshToken);
        }, delay * 1000);

        return () => clearTimeout(timeout);
    }, [context]);

    return (
        <JWTContext.Provider value={{...context, setRawToken, logout}}>
            {children}
        </JWTContext.Provider>
    );
}

export function useJWT(): JWTContextProps {
    return useContext<JWTContextProps>(JWTContext);
}

export function isTokenExpired(token: JWTToken): boolean {
    return token.payload.exp <= Math.round((new Date).getTime() / 1000);
}

export function isAuthenticated(): boolean {
    const {token} = useJWT();
    if (null === token) {
        return false;
    }

    const tokenExpired = isTokenExpired(token);

    return !tokenExpired;
}

export function useLogout() {
    const {logout} = useJWT();

    return logout;
}
