import { Authority } from "./Authority";

export interface Role {
    uuid: string;
    name: string;
    color_hex: string;
    hierarchy_level: number;
    is_shared: boolean;
    authority: Authority;
}