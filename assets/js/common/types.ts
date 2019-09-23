export type Model = {
    '@id': string,
    id: string,
}

export type HasTimestamp = {
    createdAt: string,
    updatedAt: string,
};

export type ModelCollection<T> = {
    '@context': string,
    '@id': string,
    '@type': 'hydra:Collection',
    'hydra:member': T[],
    'hydra:totalItems': number,
};
