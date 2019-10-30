import React, {createContext, ReactElement, useContext, useState} from 'react';
import enTranslations from '../i18n/en';

type Translations = {
    [key: string]: string,
};

type TranslationsMap = {
    [locale: string]: Translations,
};

type TranslationContextProps = {
    translations: TranslationsMap,
    currentLocale: string,
};

function createTranslationContext() {
    return {
        translations:  {en: enTranslations},
        currentLocale: 'en',
    };
}

const TranslationContext = createContext<TranslationContextProps>(createTranslationContext());

type Props = {
    children: ReactElement,
};

export default function TranslationProvider({children}: Props) {
    const {translations, currentLocale: defaultCurrentLocale} = createTranslationContext();
    const [currentLocale]                                     = useState<string>(defaultCurrentLocale);

    return (
        <TranslationContext.Provider value={{currentLocale, translations}}>
            {children}
        </TranslationContext.Provider>
    );
}

export function useTranslations() {
    const {translations, currentLocale} = useContext(TranslationContext);

    if ('undefined' === typeof translations[currentLocale]) {
        throw new Error('Unkown locale ' + currentLocale);
    }

    return translations[currentLocale];
}

export function trans(key: string, params: { [key: string]: string } = {}) {
    const translations = useTranslations();

    if ('undefined' === typeof translations[key]) {
        console.error(`Unkown translation string "${key}".`);
    }

    return Object.keys(params).reduce(
        (memo, paramKey) => {
            return memo.replace(`%${paramKey}%`, params[paramKey]);
        },
        translations[key],
    );
}

type TransProps = {
    children: string,
    params?: { [key: string]: string },
};

export function Trans({children, params = {}}: TransProps) {
    return <>{trans(children, params)}</>;
}
