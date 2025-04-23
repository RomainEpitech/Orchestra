export interface Authority {
    [module: string]: {
        [permission: string]: boolean;
    };
}