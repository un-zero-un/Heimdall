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

type JWTProps = { rawToken: string, token: JWTToken } | { rawToken: null, token: null };

type JWTContextProps = JWTProps & {
    setRawToken: (rawToken: string) => void,
};

const JWTContext = createContext<JWTContextProps>({
    rawToken: null, token: null, setRawToken: () => { },
});

type Props = {
    children: ReactNode,
};

export default function JWTProvider({children}: Props) {
    const [context, setContext] = useState<JWTProps>({rawToken: null, token: null});

    function setRawToken(rawToken: string) {
        const headers = jwtDecode<JWTHeaders>(rawToken, {header: true});
        const payload = jwtDecode<JWTPayload>(rawToken);

        setContext({rawToken, token: {headers, payload}});
        localStorage.setItem('jwt', rawToken);
    }

    useEffect(() => {
        const rawToken = localStorage.getItem('jwt');
        if (null === rawToken) {
            return;
        }

        const headers = jwtDecode<JWTHeaders>(rawToken, {header: true});
        const payload = jwtDecode<JWTPayload>(rawToken);
        if (isTokenExpired({headers, payload})) {
            return;
        }

        setRawToken(rawToken);
    }, []);

    return (
        <JWTContext.Provider value={{...context, setRawToken}}>
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
