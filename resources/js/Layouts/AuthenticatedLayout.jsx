import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;

    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="border-b border-blue-700 bg-blue-600">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex h-16 justify-between">
                        <div className="flex">
                            <div className="flex shrink-0 items-center">
                                <Link href="/">
                                    <ApplicationLogo className="block h-9 w-auto fill-current text-white" />
                                </Link>
                            </div>

                            <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink
                                    href={route('catalogue.index')}
                                    active={route().current('catalogue.*')}
                                >
                                    Catalogue
                                </NavLink>
                                {user && (
                                    <>
                                        <NavLink
                                            href={route('messages.index')}
                                            active={route().current('messages.*')}
                                        >
                                            Messages
                                        </NavLink>
                                        {user.role === 'Administrator' && (
                                            <>
                                                <NavLink
                                                    href={route('admin.sellers.index')}
                                                    active={route().current('admin.sellers.*')}
                                                >
                                                    Sellers
                                                </NavLink>
                                                <NavLink
                                                    href={route('admin.categories.index')}
                                                    active={route().current('admin.categories.*')}
                                                >
                                                    Categories
                                                </NavLink>
                                            </>
                                        )}
                                        {user.role === 'Seller' && (
                                            <>
                                                <NavLink
                                                    href={route('seller.listings.index')}
                                                    active={route().current('seller.listings.*')}
                                                >
                                                    My Listings
                                                </NavLink>
                                                <NavLink
                                                    href={route('seller.deliveries.index')}
                                                    active={route().current('seller.deliveries.*')}
                                                >
                                                    Deliveries
                                                </NavLink>
                                                <NavLink
                                                    href={route('seller.reports.index')}
                                                    active={route().current('seller.reports.*')}
                                                >
                                                    Reports
                                                </NavLink>
                                            </>
                                        )}
                                        {user.role === 'Buyer' && (
                                            <>
                                                <NavLink
                                                    href={route('buyer.orders.index')}
                                                    active={route().current('buyer.orders.*')}
                                                >
                                                    My Orders
                                                </NavLink>
                                                <NavLink
                                                    href={route('buyer.deliveries.index')}
                                                    active={route().current('buyer.deliveries.*')}
                                                >
                                                    Deliveries
                                                </NavLink>
                                                <NavLink
                                                    href={route('buyer.reports.index')}
                                                    active={route().current('buyer.reports.*')}
                                                >
                                                    Reports
                                                </NavLink>
                                            </>
                                        )}

                                    </>
                                )}
                            </div>
                        </div>

                        {user ? (
                            <div className="hidden sm:ms-6 sm:flex sm:items-center">
                                <div className="relative ms-3">
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <span className="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    className="inline-flex items-center rounded-md border border-transparent bg-blue-500 px-3 py-2 text-sm font-medium leading-4 text-white transition duration-150 ease-in-out hover:bg-blue-400 focus:outline-none"
                                                >
                                                    {user.name} - {user.role}

                                                    <svg
                                                        className="-me-0.5 ms-2 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </Dropdown.Trigger>

                                        <Dropdown.Content>
                                            <Dropdown.Link
                                                href={route('profile.edit')}
                                            >
                                                Profile
                                            </Dropdown.Link>
                                            <Dropdown.Link
                                                href={route('logout')}
                                                method="post"
                                                as="button"
                                            >
                                                Log Out
                                            </Dropdown.Link>
                                        </Dropdown.Content>
                                    </Dropdown>
                                </div>
                            </div>
                        ) : (
                            <div className="hidden sm:ms-6 sm:flex sm:items-center gap-4">
                                <Link
                                    href={route('login')}
                                    className="text-white hover:text-blue-200 font-medium transition"
                                >
                                    Login
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="bg-white text-blue-600 px-4 py-2 rounded-md font-medium hover:bg-blue-50 transition"
                                >
                                    Register
                                </Link>
                            </div>
                        )}

                        <div className="-me-2 flex items-center sm:hidden">
                            <button
                                onClick={() =>
                                    setShowingNavigationDropdown(
                                        (previousState) => !previousState,
                                    )
                                }
                                className="inline-flex items-center justify-center rounded-md p-2 text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:outline-none"
                            >
                                <svg
                                    className="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        className={
                                            !showingNavigationDropdown
                                                ? 'inline-flex'
                                                : 'hidden'
                                        }
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        className={
                                            showingNavigationDropdown
                                                ? 'inline-flex'
                                                : 'hidden'
                                        }
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    className={
                        (showingNavigationDropdown ? 'block' : 'hidden') +
                        ' sm:hidden'
                    }
                >
                    <div className="space-y-1 pb-3 pt-2 bg-blue-600">
                        <ResponsiveNavLink
                            href={route('catalogue.index')}
                            active={route().current('catalogue.*')}
                        >
                            Catalogue
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            href={route('messages.index')}
                            active={route().current('messages.*')}
                        >
                            Messages
                        </ResponsiveNavLink>
                        {user?.role === 'Administrator' && (
                            <>
                                <ResponsiveNavLink
                                    href={route('admin.sellers.index')}
                                    active={route().current('admin.sellers.*')}
                                >
                                    Sellers
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    href={route('admin.categories.index')}
                                    active={route().current('admin.categories.*')}
                                >
                                    Categories
                                </ResponsiveNavLink>
                            </>
                        )}
                        {user?.role === 'Seller' && (
                            <>
                                <ResponsiveNavLink
                                    href={route('seller.listings.index')}
                                    active={route().current('seller.listings.*')}
                                >
                                    My Listings
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    href={route('seller.deliveries.index')}
                                    active={route().current('seller.deliveries.*')}
                                >
                                    Deliveries
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    href={route('seller.reports.index')}
                                    active={route().current('seller.reports.*')}
                                >
                                    Reports
                                </ResponsiveNavLink>
                            </>
                        )}
                        {user?.role === 'Buyer' && (
                            <>
                                <ResponsiveNavLink
                                    href={route('buyer.orders.index')}
                                    active={route().current('buyer.orders.*')}
                                >
                                    My Orders
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    href={route('buyer.deliveries.index')}
                                    active={route().current('buyer.deliveries.*')}
                                >
                                    Deliveries
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    href={route('buyer.reports.index')}
                                    active={route().current('buyer.reports.*')}
                                >
                                    Reports
                                </ResponsiveNavLink>
                            </>
                        )}
                    </div>

                    <div className="border-t border-blue-400 pb-1 pt-4 bg-blue-600">
                        {user ? (
                            <>
                                <div className="px-4">
                                    <div className="text-base font-medium text-white">
                                        {user?.name} - {user?.role}
                                    </div>
                                    <div className="text-sm font-medium text-blue-100">
                                        {user?.email}
                                    </div>
                                </div>

                                <div className="mt-3 space-y-1">
                                    <ResponsiveNavLink
                                        href={route('profile.edit')}
                                    >
                                        Profile
                                    </ResponsiveNavLink>
                                    <ResponsiveNavLink
                                        method="post"
                                        href={route('logout')}
                                        as="button"
                                    >
                                        Log Out
                                    </ResponsiveNavLink>
                                </div>
                            </>
                        ) : (
                            <div className="px-4 space-y-1">
                                <ResponsiveNavLink
                                    href={route('login')}
                                >
                                    Login
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    href={route('register')}
                                >
                                    Register
                                </ResponsiveNavLink>
                            </div>
                        )}
                    </div>
                </div>
            </nav>

            {header && (
                <header className="bg-blue-600 shadow-lg">
                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}
