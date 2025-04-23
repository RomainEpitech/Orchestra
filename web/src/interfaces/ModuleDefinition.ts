import { MenuItem } from "./MenuItem";

export interface ModuleDefinition {
    name: string;
    permissionKey: string;
    menuItems: MenuItem[];
}