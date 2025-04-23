import { Role } from "./Role";

export interface Collaborator {
    uuid: string;
    firstname: string;
    lastname: string;
    email: string;
    role: Role;
    created_at: string;
}