import {createStore} from 'vuex';
import {userStore} from "./user";
import {teamStore} from "./team";
import {billingStore} from "./billing";
import {onboardingStore} from "./onboarding";
import {planStore} from "./plan";

export const store = createStore({
    modules: {
        userStore,
        teamStore,
        billingStore,
        onboardingStore,
        planStore,
    }
});
