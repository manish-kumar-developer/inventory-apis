
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { BreadcrumbItem, User } from '@/types';  
import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

interface Props {
  users: User[];
}
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];

export default function UsersList() {
  const { users } = usePage<{ users: User[] }>().props as Props;

  return (
     <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />
             <div className="p-6 space-y-6">
     
          <Table>
            <TableCaption>All registered users</TableCaption>
            <TableHeader>
              <TableRow>
                <TableHead className="w-1/12">ID</TableHead>
                <TableHead className="w-3/12">Name</TableHead>
                <TableHead className="w-4/12">Email</TableHead>
                <TableHead className="w-2/12">Joined</TableHead>
                <TableHead className="w-2/12">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {users.map((user) => (
                <TableRow key={user.id} className="hover:bg-gray-100 dark:hover:bg-gray-600  transition">
                  <TableCell>{user.id}</TableCell>
                  <TableCell>{user.name}</TableCell>
                  <TableCell>{user.email}</TableCell>
                  <TableCell>
                    {new Date(user.created_at).toLocaleDateString('en-IN', {
                      year: 'numeric',
                      month: 'short',
                      day: 'numeric',
                    })}
                  </TableCell>
                  <TableCell className="space-x-2">
                    <Button
                      size="sm"
                      variant="outline"
                      className='cursor-pointer'
                    //   onClick={() => route().visit(route('users.show', user.id))}
                    >
                      View
                    </Button>
                    <Button
                      size="sm"
                      variant="ghost"
                      className=' cursor-pointer'
                    //   onCwlick={() => route().visit(route('users.edit', user.id))}
                    >
                      Edit
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
    
    </div>
   </AppLayout>
  );
}
