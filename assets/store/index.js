import { createStore } from 'vuex';
import {userStore} from "./user";
import {teamStore} from "./team";
import {billingStore} from "./billing";

export const store = createStore({
    modules: {
        userStore,
        teamStore,
        billingStore,
    }
});
