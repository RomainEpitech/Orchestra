import { Enterprise } from "./Enterprise";
import { Role } from "./Role";

export interface User {
    uuid: string;
    firstname: string;
    lastname: string;
    email: string;
    avatar?: string | null;
    role: Role;
    enterprise: Enterprise;
}