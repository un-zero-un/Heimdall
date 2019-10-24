import React, {createContext, ReactNode, useContext, useEffect} from 'react';
import {Model} from './types';

type Props = {
    children: ReactNode,
    hubUrl: string,
    topics: string[],
};

const MercureContext = createContext([new EventTarget]);

export default function MercureProvider({hubUrl, topics, children}: Props) {
    const dispatcher = new EventTarget;
    const url        = new URL(hubUrl);
    topics.forEach(topic => url.searchParams.append('topic', topic));

    const eventSource     = new EventSource(url.href);
    eventSource.onmessage = e => {
        dispatcher.dispatchEvent(new MessageEvent(
            'message',
            {
                data: JSON.parse(e.data),
                lastEventId: e.lastEventId,
                origin: e.origin
            },
        ));
    };

    return (
        <MercureContext.Provider value={[dispatcher]}>
            {children}
        </MercureContext.Provider>
    );
}

export function useMercure() {
    return useContext(MercureContext);
}

export function useMercureMessages(listener: EventListener) {
    const [dispatcher] = useMercure();
    dispatcher.addEventListener('message', listener);

    useEffect(() => () => dispatcher.removeEventListener('message', listener));
}

export function subscribeToMercureResource<T extends Model>(type: string, listener: (model: T) => void) {
    useMercureMessages(function (e) {
        const message = e as MessageEvent;
        const model = message.data as Model;

        if (model['@type'] === type) {
            listener(message.data);
        }
    });
}
