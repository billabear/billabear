
import {userservice} from "../userservice";

import axios from "axios";
import MockAdapter from "axios-mock-adapter";
import { describe, it, expect, beforeAll, afterEach } from 'vitest'

// This sets the mock adapter on the default instance
var mock = new MockAdapter(axios);

describe("userService", () => {
    let mock;

    beforeAll(() => {
        mock = new MockAdapter(axios);
        axios.defaults.validateStatus = function () {
            return true;
        };
    });

    afterEach(() => {
        mock.reset();
    });

    describe("Fetch user settings", () => {
        it("Should return response if successful", async () => {
            mock.onGet(`/api/user/settings`).reply(200, {form: {success: true}});

            // when
            const result = await userservice.fetchSettings();

            // then
            expect(mock.history.get[0].url).toEqual(`/api/user/settings`);
            expect(result).toEqual({success: true});
        });

        it("Should return error", async () => {
            mock.onGet(`/api/user/settings`).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.fetchSettings();
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.get[0].url).toEqual(`/api/user/settings`);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("Update user settings", () => {
        it("Should return response if successful", async () => {
            const user = {
                name: "Name",
                email: "iain.cambridge@example.org",
            }

            mock.onPost(`/api/user/settings`, user).reply(200, {success: true});

            // when
            const result = await userservice.updateSettings(user);

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/settings`);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {
            const user = {
                name: "Name",
                email: "iain.cambridge@example.org",
            }

            mock.onPost(`/api/user/settings`, user).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.updateSettings(user);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/settings`);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("When doing confirm email", () => {
        it("Should return response if successful", async () => {

            var code = "a-random-code";

            mock.onGet(`/api/user/confirm/`+code).reply(200, {success: true});

            // when
            const result = await userservice.confirmEmail(code)

            // then
            expect(mock.history.get[0].url).toEqual(`/api/user/confirm/`+code);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var code = "a-random-code";

            mock.onGet(`/api/user/confirm/`+code).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.confirmEmail(code);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.get[0].url).toEqual(`/api/user/confirm/`+code);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("When doing reset password check", () => {
        it("Should return response if successful", async () => {

            var code = "a-random-code";

            mock.onGet(`/api/user/reset/`+code).reply(200, {success: true});

            // when
            const result = await userservice.forgotPasswordCheck(code)

            // then
            expect(mock.history.get[0].url).toEqual(`/api/user/reset/`+code);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var code = "a-random-code";

            mock.onGet(`/api/user/reset/`+code).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.forgotPasswordCheck(code);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.get[0].url).toEqual(`/api/user/reset/`+code);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("When sending the new password during the reset password", () => {
        it("Should return response if successful", async () => {

            var code = "a-random-code";
            var newPassword = "a-new-password";
            mock.onPost(`/api/user/reset/`+code, {password: newPassword}).reply(200, {success: true});

            // when
            const result = await userservice.forgotPasswordConfirm(code, newPassword)

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/reset/`+code);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var code = "a-random-code";
            var newPassword = "a-new-password";

            mock.onPost(`/api/user/reset/`+code, {password: newPassword}).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.forgotPasswordConfirm(code, newPassword);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/reset/`+code);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("Change Password", () => {
        it("Should return response if successful", async () => {

            var password = "a-random-password";
            var new_password = "new.password";
            mock.onPost(`/api/user/password`, {password, new_password}).reply(200, {success: true});

            // when
            const result = await userservice.changePassword(password, new_password)

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/password`);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var password = "a-random-password";
            var new_password = "new.password";
            mock.onPost(`/api/user/password`, {password, new_password}).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.changePassword(password, new_password);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/password`);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("User Invite", () => {
        it("Should return response if successful", async () => {

            var code = "a-random-code";
            var email = "iain.cambridge@example.org";
            mock.onPost(`/api/user/invite`, {email}).reply(200, {success: true});

            // when
            const result = await userservice.invite( email)

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/invite`);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var code = "a-random-code";
            var email = "iain.cambridge@example.org";

            mock.onPost(`/api/user/invite`, {email}).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.invite(email);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/invite`);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("When starting the forgot password process", () => {
        it("Should return response if successful", async () => {

            var code = "a-random-code";
            var email = "iain.cambridge@example.org";
            mock.onPost(`/api/user/reset`, {email}).reply(200, {success: true});

            // when
            const result = await userservice.forgotPassword( email)

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/reset`);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var code = "a-random-code";
            var email = "iain.cambridge@example.org";

            mock.onPost(`/api/user/reset`, {email}).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.forgotPassword(email);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/reset`);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("The user sign up", () => {
        it("Should return response if successful", async () => {

            var user =  {
                username: "iain.cambridge@example.org",
                password: "a-password"
            };

            mock.onPost(`/api/user/signup`, user).reply(200, {success: true});

            // when
            const result = await userservice.signup(user, undefined);

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/signup`);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var user =  {
                username: "iain.cambridge@example.org",
                password: "a-password"
            };

            mock.onPost(`/api/user/signup`, user).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.signup(user, undefined);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/signup`);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("The user sign up with invite code", () => {
        it("Should return response if successful", async () => {

            var user =  {
                username: "iain.cambridge@example.org",
                password: "a-password"
            };
            var code = 'invite-code';

            mock.onPost(`/api/user/signup/`+code, user).reply(200, {success: true});

            // when
            const result = await userservice.signup(user, code);

            // then
            expect(mock.history.post[0].url).toEqual(`/api/user/signup/`+code);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var user =  {
                username: "iain.cambridge@example.org",
                password: "a-password"
            };
            var code = 'invite-code';

            mock.onPost(`/api/user/signup/`+code, user).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.signup(user, code);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/user/signup/`+code);
                expect(error).toEqual("Invalid code");

            }
        });
    });

    describe("The login", () => {
        it("Should return response if successful", async () => {

            var user =  {
                username: "iain.cambridge@example.org",
                password: "a-password"
            };

            mock.onPost(`/api/authenticate`, user).reply(200, {success: true});

            // when
            const result = await userservice.login(user.username, user.password);

            // then
            expect(mock.history.post[0].url).toEqual(`/api/authenticate`);
            expect(result.data).toEqual({success: true});
        });

        it("Should return error", async () => {

            var user =  {
                username: "iain.cambridge@example.org",
                password: "a-password"
            };

            mock.onPost(`/api/authenticate`, user).reply(400, {success: false, error: "Invalid code"});

            try {
                await  userservice.login(user.username, user.password);
                fail("Didn't throw error")
            } catch (error) {
                expect(mock.history.post[0].url).toEqual(`/api/authenticate`);
                expect(error).toEqual("Invalid code");

            }
        });
    });
})